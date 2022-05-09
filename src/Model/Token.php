<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

final class Token 
{
    private int $token_id;
    private string $token_value;
    private bool $is_redeemed;
    private int $user_id;

    public function _construct(
        string $token_value,
        bool $is_redeemed,
        int $user_id
    ){
        $this->token_value = $token_value;
        $this->is_redeemed = $is_redeemed;
        $this->user_id = $user_id;
    }

    public function getTokenId(): int{
        return $this->token_id;
    }

    /**
     * @param int $token_id
     */
    public function setTokenId(int $token_id): void
    {
        $this->token_id = $token_id;
    }

    /**
     * @return string
     */
    public function getTokenValue(): string
    {
        return $this->token_value;
    }

    /**
     * @param string $token_value
     */
    public function setTokenValue(string $token_value): void
    {
        $this->token_value = $token_value;
    }

    /**
     * @return bool
     */
    public function isIsRedeemed(): bool
    {
        return $this->is_redeemed;
    }

    /**
     * @param bool $is_redeemed
     */
    public function setIsRedeemed(bool $is_redeemed): void
    {
        $this->is_redeemed = $is_redeemed;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }
}